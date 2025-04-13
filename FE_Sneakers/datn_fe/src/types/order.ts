export interface OrderDetail {
  id: number
  product_id: number
  product_name: string
  variant_id: number
  size: string
  quantity: number
  price: number
  image_url: string
}

export interface Order {
  id: number
  order_code: string
  recipient_name: string
  recipient_phone: string
  recipient_address: string
  promotion: number
  shipping_fee: number
  total_price: number
  payment_method: string
  payment_status: string
  status: string
  cho_xac_nhan_huy: string | null
  previous_status: string | null
  created_at: string
  updated_at: string
  order_details: OrderDetail[]
}

export interface PaginatedOrders {
  data: Order[]
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    from: number
    last_page: number
    path: string
    per_page: number
    to: number
    total: number
  }
}
