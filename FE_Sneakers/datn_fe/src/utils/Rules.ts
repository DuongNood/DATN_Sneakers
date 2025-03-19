import type { RegisterOptions, UseFormGetValues } from 'react-hook-form'
import * as yup from 'yup'
import { AnyObject } from 'yup'

type Rules = {
  [key in 'email' | 'password' | 'confirm_password']?: RegisterOptions
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const getRules = (getValues?: UseFormGetValues<any>): Rules => ({
  email: {
    required: {
      value: true,
      message: 'Vui lòng nhập Email!'
    },
    pattern: {
      value: /^\S+@\S+\.\S+$/,
      message: 'Email không đúng định dạng!'
    },
    maxLength: {
      value: 160,
      message: 'Không được vượt quá 160 kí tự!'
    },
    minLength: {
      value: 5,
      message: 'Không được nhỏ hơn 5 kí tự!'
    }
  },
  password: {
    required: {
      value: true,
      message: 'Vui lòng nhập mật khẩu!'
    },
    maxLength: {
      value: 160,
      message: 'Mật khẩu không được dài quá 160 kí tự!'
    },
    minLength: {
      value: 6,
      message: 'Mật khẩu ngắn nhất là 6 kí tự!'
    }
  },
  confirm_password: {
    required: {
      value: true,
      message: 'Vui lòng nhập lại mật khẩu!'
    },
    maxLength: {
      value: 160,
      message: 'Mật khẩu không được dài quá 160 kí tự!'
    },
    minLength: {
      value: 6,
      message: 'Mật khẩu ngắn nhất là 6 kí tự!'
    },
    validate:
      typeof getValues === 'function'
        ? (value) => value === getValues('password') || 'Mật khẩu không khớp nhau!'
        : undefined
  }
})

function testPriceMinMax(this: yup.TestContext<AnyObject>) {
  const { price_max, price_min } = this.parent as {
    price_min: string
    price_max: string
  }
  if (price_min !== '' && price_max !== '') {
    return Number(price_max) >= Number(price_min)
  }
  return price_min !== '' || price_max !== ''
}

const handleConfirmPasswordYup = (refString: string) => {
  return yup
    .string()
    .required('Vui lòng nhập mật khẩu!')
    .min(6, 'Mật khẩu ngắn nhất là 6 kí tự!')
    .max(160, 'Mật khẩu không được dài quá 160 kí tự!')
    .oneOf([yup.ref(refString)], 'Mật khẩu không khớp nhau!')
}

export const schema = yup.object({
  email: yup
    .string()
    .required('Vui lòng nhập Email!')
    .email('Email không đúng định dạng@!')
    .min(5, 'Độ dài từ 5 - 160 ký tự!')
    .max(160, 'Độ dài từ 5 - 160 ký tự!'),
  password: yup
    .string()
    .required('Vui lòng nhập mật khẩu!')
    .min(6, 'Độ dài từ 6 - 160 ký tự')
    .max(160, 'Độ dài từ 6 - 160 ký tự'),
  confirm_password: handleConfirmPasswordYup('password'),
  price_min: yup.string().test({
    name: 'price-not-allowed',
    message: 'Giá không phù hợp',
    test: testPriceMinMax
  }),
  price_max: yup.string().test({
    name: 'price-not-allowed',
    message: 'Giá không phù hợp',
    test: testPriceMinMax
  }),
  name: yup.string().trim().required('Đã tìm kiếm gì đâu mà search hả cụ nội!')
})

export const userSchema = yup.object({
  name: yup.string().max(160, 'Độ dài tối đa là 160 ký tự'),
  phone: yup.string().max(20, 'Độ dài tối đa là 20 ký tự'),
  address: yup.string().max(160, 'Độ dài tối đa là 160 ký tự'),
  avatar: yup.string().max(1000, 'Độ dài tối đa là 1000 ký tự'),
  date_of_birth: yup.date().max(new Date(), 'Hãy chọn một ngày trong quá khứ'),
  password: schema.fields['password'],
  new_password: schema.fields['password'],
  confirm_password: handleConfirmPasswordYup('new_password')
})

export type UserSchema = yup.InferType<typeof userSchema>

export type Schema = yup.InferType<typeof schema>
