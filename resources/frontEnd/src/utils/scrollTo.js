const homeSections = new Set(['hero', 'services'])

const routeMap = {
  about: 'about',
  contact: 'contact',
  projects: 'projects',
}

export function scrollToSection(id, router, route) {
  if (!id) return

  if (homeSections.has(id)) {
    const isHome = route.name === 'home'
    if (isHome) {
      const el = document.getElementById(id)
      if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' })
      }
    } else {
      router.push({ name: 'home', hash: `#${id}` })
    }
    return
  }

  const routeName = routeMap[id]
  if (routeName) {
    router.push({ name: routeName })
  }
}
