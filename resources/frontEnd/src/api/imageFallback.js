const BASE = import.meta.env.BASE_URL || "/";

function fromStoragePath(path) {
  if (path.startsWith("work-samples/")) {
    return `${BASE}images/work-samples-images/${path.slice("work-samples/".length)}`;
  }
  return `${BASE}images/${path}`;
}

export function localImageUrl(url) {
  if (!url) return "";
  const str = String(url);
  const storageMatch = str.match(/\/storage\/(.+)$/);
  if (storageMatch) return fromStoragePath(storageMatch[1]);
  if (str.startsWith("work-samples/")) {
    return fromStoragePath(str);
  }
  return str;
}
