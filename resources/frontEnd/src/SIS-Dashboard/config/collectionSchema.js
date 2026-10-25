export const COLLECTION_SCHEMA = {
  services: {
    label: "Services",
    single: "Service",
    icon: "engineering",
    primary: "title",
    searchFields: ["title", "summary", "description"],

    fields: [
      {
        key: "title",
        label: "Title",
        type: "text",
        required: true,
      },
      {
        key: "headline",
        label: "Headline / Subtitle",
        type: "text",
      },
      {
        key: "slug",
        label: "Slug",
        type: "text",
      },
      {
        key: "icon",
        label: "Icon",
        type: "text",
      },
      {
        key: "summary",
        label: "Summary",
        type: "textarea",
      },
      {
        key: "description",
        label: "Description",
        type: "textarea",
      },
    ],
  },

  sectors: {
    label: "Industries",
    single: "Industry",
    icon: "factory",
    primary: "name",
    searchFields: ["name", "slug", "description"],

    fields: [
      {
        key: "name",
        label: "Name",
        type: "text",
        required: true,
      },
      {
        key: "slug",
        label: "Slug",
        type: "text",
      },
      {
        key: "icon",
        label: "Icon",
        type: "text",
      },
      {
        key: "description",
        label: "Description",
        type: "textarea",
      },
      {
        key: "relevant_programs",
        label: "Relevant Programs",
        type: "array",
      },
      {
        key: "typical_scope",
        label: "Typical Scope",
        type: "array",
      },
    ],
  },

  partners: {
    label: "Partners",
    single: "Partner",
    icon: "handshake",
    primary: "name",
    searchFields: ["name", "slug", "type", "website_url"],

    fields: [
      {
        key: "name",
        label: "Name",
        type: "text",
        required: true,
      },
      {
        key: "slug",
        label: "Slug",
        type: "text",
      },
      {
        key: "type",
        label: "Type",
        type: "select",
        options: [
          {
            label: "Technology Partner",
            value: "technology_partner",
          },
          {
            label: "Ecosystem Network",
            value: "ecosystem_network",
          },
        ],
      },
      {
        key: "website_url",
        label: "Website URL",
        type: "url",
      },
      {
        key: "logo_path",
        label: "Logo",
        type: "image",
        fromKey: "logo_url",
      },
      {
        key: "sort_order",
        label: "Sort Order",
        type: "number",
      },
    ],
  },

 clients: {
  label: "Clients",
  single: "Client",
  icon: "business",
  primary: "name",

  searchFields: ["name"],

  columns: [
    {
      key: "logo_url",
      label: "Logo",
      type: "image",
    },
    {
      key: "name",
      label: "Name",
    },
    {
      key: "sort_order",
      label: "Order",
    },
  ],

  fields: [
    {
      key: "name",
      label: "Name",
      type: "text",
      required: true,
    },
    {
      key: "logo_path",
      label: "Logo",
      type: "image",
      fromKey: "logo_url",
    },
    {
      key: "sort_order",
      label: "Sort Order",
      type: "number",
    },
  ],
},
};