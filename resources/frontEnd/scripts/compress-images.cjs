const sharp = require('sharp')
const path = require('path')
const fs = require('fs')

const IMAGES_DIR = path.resolve(__dirname, '..', 'src', 'images')
const QUALITY = 80
const MAX_WIDTH = 1920

const extensions = new Set(['.jpg', '.jpeg', '.png'])
const results = { converted: 0, skipped: 0, errors: [] }

async function convertFile(filePath) {
  const ext = path.extname(filePath).toLowerCase()
  if (!extensions.has(ext)) return false

  const webpPath = filePath.replace(/\.(jpg|jpeg|png)$/i, '.webp')
  if (fs.existsSync(webpPath)) {
    results.skipped++
    return false
  }

  try {
    const image = sharp(filePath)
    const metadata = await image.metadata()

    let pipeline = image
    if (metadata.width > MAX_WIDTH) {
      pipeline = pipeline.resize({ width: MAX_WIDTH, withoutEnlargement: true })
    }

    await pipeline
      .webp({ quality: QUALITY })
      .toFile(webpPath)

    const origSize = (fs.statSync(filePath).size / 1024 / 1024).toFixed(2)
    const newSize = (fs.statSync(webpPath).size / 1024 / 1024).toFixed(2)
    const savings = ((1 - newSize / origSize) * 100).toFixed(1)

    console.log(`  ✓ ${path.basename(filePath)} (${origSize}MB → ${newSize}MB, ${savings}% saved)`)
    results.converted++
    return true
  } catch (err) {
    results.errors.push(`${filePath}: ${err.message}`)
    return false
  }
}

async function walkDir(dir) {
  const entries = fs.readdirSync(dir, { withFileTypes: true })
  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name)
    if (entry.isDirectory()) {
      await walkDir(fullPath)
    } else if (entry.isFile()) {
      await convertFile(fullPath)
    }
  }
}

console.log('\n🔍 Scanning for images...\n')
walkDir(IMAGES_DIR).then(() => {
  console.log(`\n📊 Summary:`)
  console.log(`   Converted: ${results.converted}`)
  console.log(`   Skipped (already exists): ${results.skipped}`)
  if (results.errors.length) {
    console.log(`   Errors: ${results.errors.length}`)
    results.errors.forEach(e => console.log(`     ⚠ ${e}`))
  }
  console.log()
})
